import { useMutation } from "@tanstack/vue-query";
import { useQueryClient } from "@tanstack/vue-query";
import { useToast } from "vue-toastification";
import axios from "axios";

const toast = useToast();

export const uploadImg = async (imageFile) => {
    const formData = new FormData();
    formData.append("image", imageFile);
    try {
        const response = await axios.post(`/save`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        return response.data;
    } catch (error) {
        throw new Error(
            error.response?.data?.message || "Failed to upload image"
        );
    }
};

export const useUploadImgMutation = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: uploadImg,
        onSuccess: () => {
            queryClient.invalidateQueries(["products", "product-items"]);
            toast.success("Image Uploaded Successfully!");
        },
        onError: (error) => {
            if (error.errors) {
                Object.values(error?.errors)
                    .flat()
                    .forEach((err) => {
                        toast.error(err);
                    });
            } else {
                toast.error(error?.message || "An error occurred");
            }
        },
    });
};
